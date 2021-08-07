function UpdateManager( url, sources, pkgs ) {
	var packages = {};
	var install = [];
	var queue = [];

	var totalcount = 0;
	var totalcompleted = 0;

	for( var i in pkgs ) {
		var pkg = pkgs[i];
		pkg.remote_version = '';
		packages[pkg.id] = pkg;
	}

	/**
	 * Add remote package information
	 * @param s server id
	 * @param p packages
	 */
	this.remote = function( s, p ) {
		for( var i = 0; i < p.length; i++ ) {
			var pkg = p[i];
			pkg.remote_version = pkg.version;
			pkg.source = sources[s];

			delete pkg.version;
			delete pkg.forward;

			if( packages[pkg.id] ) $.extend( packages[pkg.id], pkg );
			else packages[pkg.id] = pkg;
		}
	};

	/**
	 * Add a list of packages to the given element
	 * @param target
	 */
	this.displayPackages = function( target ) {
		var table = $( '<table class="table table-striped">' );
		var self = this;
		var updates = [];

		table.append( $( '<tr><th>Name</th><th>Source</th><th>Local Version</th><th>Remote Version</th><th>Aktionen</th></tr>' ))

		for( var i in packages )
			(function( pkg ) {
				var actions = $( '<td></td>' );

				function install() {
					$('#progressmodal').show();
					self.installPackages( [pkg.id] );
					return false;
				}

				if( !pkg.source || pkg.forward == "1" ) {
					var link = $( '<a class="btn btn-small">Bearbeiten</a> ' );
					link.attr( 'href', url+'&edit='+pkg.id );
					actions.append( link );
				}

				if( pkg.remote_version )
					if( pkg.version && Number(pkg.version) < Number(pkg.remote_version )) {
						var link = $( '<a href="#" class="btn btn-small">Aktuallisieren</a>' );
						updates.push( pkg.id );
						link.click( install );
						actions.append( link );
					} else if( !pkg.version ) {
						var link = $( '<a href="#" class="btn btn-small">Installieren</a>' );
						link.click( install );
						actions.append( link );
					}

				var source = pkg.source ? pkg.source.name : 'Lokal';
				table.append( $( '<tr><td>'+pkg.name+'</td><td>'+source+'</td><td>'+( pkg.version ? pkg.version : 0 )+'</td><td>'+pkg.remote_version+'</td></tr>' ).append( actions ))
			})( packages[i] );

		var updateAllButton = $( '<button class="btn">Alle Aktuallisieren</button>' );
		updateAllButton.click( function() {
			$('#progressmodal').show();
			$('#totalmodal').show();
			self.installPackages( updates );
			return false;
		});

		if( updates.length )
			target.append( $('<p></p>').append( updateAllButton.clone(true)));

		target.append( table );

		if( updates.length )
			target.append( $('<p></p>').append( updateAllButton.clone(true)));
	};

	function updateProgress( bar, count, total ) {
		var width = Math.round( count / total * 100 );
		bar.css('width', width+'%');
	}

	function info( msg ) {
		$( '#log > div').append(msg+'<br>');
		$( '#log' ).scrollTop( $( '#log > div' ).height());
	}

	function error( msg ) {
		info('<div class="alert alert-danger">'+msg+'</div>');
	}

	this.error = function( serverid, data ) {
		error( data.error );
	};

	var callback = null;

	function loadPackage(id) {
		var pkg = packages[id];
		if( pkg ) {
			$.getScript( pkg.source.url+
				'?interface=iv.exchange&package='+pkg.id+
				'&serverid='+pkg.source.id+'&current='+pkg.version );
		} else {
			error('Paket nicht gefeunden: '+id);
		}
	}

	function runMigrations() {
		$.getJSON(url+'&getmigrations', function( data ) {
			info('Installing migrations');
			var migration = new MigrationManager( url, data, $( '#pkgpgrs' ), $('#log'));
			migration.installAll();
		});
	}

	function uploadComplete( data ) {
		if( data != 'ok' ) {
			error( data );
		} else {
			info('  Package Completed');
			if( callback ) callback();
		}
	}

	function uploadPackage(serverid, pkg) {
		var count = pkg.files.length;
		var next, completed = 0;

		var progressbar = $( '#pkgpgrs' );
		info('Starting installation of '+pkg.id);

		while( next = pkg.files.shift()) (function(file) {
			file.pkg = pkg.id;

			$.post( url+'&storefile', file, function( data ) {
				if( data != 'ok' ) {
					error( data );
				} else {
					updateProgress( progressbar, ++completed, count);
					info('  Stored '+file.path);

					if( completed == count ) {
						var pkginfo = {
							id: pkg.id,
							source: serverid,
							version: pkg.version,
							forward: packages[pkg.id].source.forward
						};

						$.post( url+'&addpackage', pkginfo, uploadComplete );
					}
				}
			});
		})(next);
	}

	function checkCycle(p, stack) {
		if(stack.indexOf(p) >= 0)
			throw "Cyclic Reference Detected";

		var ns = [p];
		for(var i in stack)
			ns.push(stack[i]);

		for(var d in pkgs[p].dependencies)
			checkCycle(d, ns);
	}

	/**
	 * Installs all packaes in the list
	 * @param p packages
	 */
	this.installPackages = function( p ) {
		install = p;
		totalcount = p.length;
		totalcompleted = 0;

		callback = function() {
			updateProgress( $( '#totalpgrs' ), ++totalcompleted, totalcount );

			if(install.length) {
				var id = install.shift();
				loadPackage(id);
			} else if(queue.length) {
				var pkg = queue.pop();
				uploadPackage(pkg.server, pkg.pkg);
			} else {
				callback = null;
				runMigrations();
			}
		};

		callback();
	};

	/**
	 * Install a single package
	 * This function is called by the remote exchange interface
	 * @param serverid
	 * @param pkg
	 */
	this.install = function( serverid, pkg ) {
		var fulfilled = true;

		for(var d in pkg.dependencies) {
			checkCycle(d, []);

			if(pkgs[d].version < pkg.dependencies[d]) {
				fulfilled = false;
				install.push(d);
				totalcount++;
			}
		}

		if(fulfilled) {
			uploadPackage(serverid, pkg);
		} else {
			queue.push({server: serverid, pkg: pkg});
			callback();
		}
	}
}
