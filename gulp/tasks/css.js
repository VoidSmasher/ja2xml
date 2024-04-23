var path = require('path'),
	gulp = require('gulp'),
    sass = require('gulp-sass'),
    sourcemaps = require('gulp-sourcemaps'),
    autoprefixer = require('gulp-autoprefixer'),
    cssmin = require('gulp-cssmin'),
	handleErrors = require('../util/handleErrors');

var config = require('../config');

gulp.task('css', function() {
    for(var asset in config.tasks.css) {
    	for(var file in config.tasks.css[asset]) {
			(function(file,asset,options){
				var src_filepath = path.join(config.src_path, 'css', asset, file + '.scss'),
					trg_folder = path.join(config.trg_path, asset, 'css');

				return gulp.src(src_filepath) // get src from config
					.pipe(sourcemaps.init())
					.pipe(sass().on('error', sass.logError))
					.pipe(sourcemaps.write())
					.on('error', handleErrors) // handle errors
					.pipe(autoprefixer({
						browsers: ['> 0%'],
						cascade: false
					})) // autoprefix postprocessor
					.pipe(cssmin())
					.pipe(gulp.dest(trg_folder)); // write to destination
			})(file,asset,config.tasks.css[asset][file]);
		}
	}
});
