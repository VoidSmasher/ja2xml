var path = require('path'),
    gulp = require('gulp'),
    watch = require('gulp-watch'),
    gutil = require('gulp-util'),
    browserSync = require('browser-sync');

var config = require('../config');

gulp.task('default', function() {

  var watch_paths = {},
      sync_files = [];

  for(var task in config.tasks) {
    for(var asset in config.tasks[task] ) {

        for(var file in config.tasks[task][asset]) {
            var filepath = path.join(config.trg_path, asset, task, file + '.' + task);
            sync_files.push( filepath );
        }

        if( typeof watch_paths[task] == 'undefined' ) {
            watch_paths[task] = [];
        }

        switch(task) {
            case 'css':
                watch_paths[task].push( path.join(config.src_path, task, '**/*.scss') );
                break;
            case 'svg':
                watch_paths[task].push( path.join(config.src_path, task, '**/*.svg') );
                break;
        }
    }
  }

  for(var task in watch_paths) {
    if(watch_paths[task].length) {
      (function(task){
        watch(watch_paths[task], function() {
          gulp.start(task);
        });
      })(task);
    }
  }

  global.isWatchify = true; // set watchify listening
  gulp.start(['js','css','svg']);

  config.browser_sync.files = sync_files;
  browserSync.init(config.browser_sync);
});
