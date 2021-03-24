'use strict';

var gulp = require('gulp');
var sass = require('gulp-sass');
var autoprefixer = require('gulp-autoprefixer');
var rename = require('gulp-rename');

gulp.task('sass', function() {
  return gulp
    .src('./scss/**/*.scss')
    .pipe(sass({
      outputStyle: 'expanded',
      indentWidth: 4
    }).on('error', sass.logError))
    .pipe(autoprefixer())
    .pipe(rename('styles.css'))
    .pipe(gulp.dest('./'));
});

gulp.task('sass:watch', function() {
  gulp.watch('./scss/**/*.scss', ['sass']);
});
