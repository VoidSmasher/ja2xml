var path = require('path'),
	gulp = require("gulp"),
	gutil = require('gulp-util'),
    svgSprite = require("gulp-svg-sprite"),
    handleErrors = require('../util/handleErrors');

var config = require('../config');

gulp.task('svg', function () {
    for(var asset in config.tasks.svg) {
      for(var folder in config.tasks.svg[asset]) {
      	(function(folder,asset,options){
			var source_filemask = path.join(config.src_path, 'svg', asset, folder, '*.svg'),
				target_folder = path.join(config.trg_path, asset, 'svg');

			return gulp.src(source_filemask)
      			.pipe(svgSprite({
			      mode: {
			        symbol: {
			          sprite          : folder + '.svg',
			          dest            : '',
			          example : true
			        }
			      },
			      svg : {
			        xmlDeclaration: false,
			        doctypeDeclaration : false
			      }
			    }))
      			.pipe(gulp.dest(target_folder));
      	})(folder,asset,config.tasks.svg[asset][folder]);
      }
  	}
});