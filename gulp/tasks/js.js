var gulp = require('gulp'),
	path = require('path'),
	browserify = require('browserify'),
	watchify = require('watchify'),
	babelify = require('babelify'),
	source = require('vinyl-source-stream'),
	buffer = require('vinyl-buffer'),
	uglify = require('gulp-uglify'),
	handleErrors = require('../util/handleErrors'),
	bundleLogger = require('../util/bundleLogger');

var config = require('../config');

gulp.task('js',function(){
	for(var asset in config.tasks.js) {
		for(var file in config.tasks.js[asset]) {
			(function(file,asset,options){
				var bundle,
					bundler,
					filename = file + '.js',
					src_filepath = path.join(config.src_path, 'js', asset, filename),
					trg_folder = path.join(config.trg_path, asset, 'js'),
					browserify_config = {
						cache: {},
						packageCache: {},
						entries : src_filepath,
						fullPaths: false,
						extensions: ['.js']
					};

				bundler = browserify(browserify_config).transform(babelify);

				if (global.isWatchify) {
					bundler = watchify(bundler);
				}

				bundle = function() {
					bundleLogger.start(browserify_config.entries); // start logging

					return bundler
						.bundle()
						.on('error', handleErrors) // handle errors
						.pipe(source(filename)) // set source dest
						// .pipe(buffer())
						// .pipe(uglify())
						.pipe(gulp.dest(trg_folder)) // write to dest
						.on('end', function() {
							bundleLogger.end(browserify_config.entries); // log that everything is bundled
						});
				};

				bundler.on('update', bundle); // set listener to bundle

				return bundle();
			})(file,asset,config.tasks.js[asset][file]);
		}
	}
});
