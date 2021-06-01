const { src, dest, series, parallel, watch } = require('gulp');
const uglify = require('gulp-uglify');
const rename = require('gulp-rename');
const clean = require('gulp-clean');
const sass = require('gulp-sass');
sass.compiler = require('node-sass');
const cleanCSS = require('gulp-clean-css');
const sourcemaps = require('gulp-sourcemaps');
const image = require('gulp-image');
const autoprefixer = require('gulp-autoprefixer');
const browserSync = require('browser-sync').create();

function jsLibraries() {
  return src([
      'node_modules/jquery/dist/jquery.min.js',
      'src/vendors/jquery-ui-1.12.1/jquery-ui.min.js',
      'node_modules/animejs/lib/anime.min.js',
      'node_modules/maptalks/dist/maptalks.min.js',
      'node_modules/maptalks.formats/dist/maptalks.formats.min.js',
      'node_modules/malihu-custom-scrollbar-plugin/jquery.mCustomScrollbar.concat.min.js',
      'src/vendors/slick-1.8.1/slick/slick.min.js',
      'src/vendors/aframe/aframe-master.min.js',
      'src/vendors/aframe/aframe-orbit-controls-component.min.js',
      'src/vendors/simplebar/simplebar.min.js',
      'src/vendors/geocoordsparser.js'
    ]).pipe(dest('build/js/vendors'));
}

function js () {
  return src('src/js/script.js')
    .pipe(dest('build/js'))
    .pipe(sourcemaps.init())
    .pipe(uglify())
    .pipe(rename({ suffix: '.min' }))
    .pipe(sourcemaps.write())
    .pipe(dest('build/js'))
    .pipe(browserSync.stream());
}

function scss () {
  return src('src/scss/main.scss')
    .pipe(sass({ allowEmpty: true }).on('error', sass.logError))
    .pipe(autoprefixer())
    .pipe(rename({ basename: 'style' }))
    .pipe(dest('build/css'))
    .pipe(sourcemaps.init())
    .pipe(cleanCSS({compatibility: 'ie11'}))
    .pipe(rename({ suffix: '.min' }))
    .pipe(sourcemaps.write())
    .pipe(dest('build/css'))
    .pipe(browserSync.stream());
}

function images () {
  return src('src/images/**/*.*')
    .pipe(dest('build/images'))
    .pipe(browserSync.stream());
}

function fonts () {
  return src('src/fonts/**/*.*')
    .pipe(dest('build/fonts'))
    .pipe(browserSync.stream());
}

function reload (cb) {
  browserSync.reload();
  cb();
}

function watcher () {
  watch('src/scss/**/*.scss', series(scss, reload));
  watch('src/js/**/*.js', series(js, reload));
  watch('src/images/**/*.js', series(images, reload));
  watch('./*.html', reload);
}

function serve () {
  browserSync.init({
    server: "./",
    open: true,
    notify: false
  });
  watcher();
}

function clear () {
  return src(['build/css', 'build/fonts', 'build/js'], {read: false})
    .pipe(clean());
}

function dev () {
  return series(clear, jsLibraries, js, fonts, scss, serve);
}

function build () {
  return series(clear, parallel(jsLibraries, images, js, fonts, scss));
}

function img () {
  return series(images)
}

exports.images = img();
exports.build = build();
exports.dev = dev();
