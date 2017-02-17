var gulp = require('gulp');
var uglify = require('gulp-uglify');
var concat = require('gulp-concat');
var sass = require('gulp-sass');
var watch = require('gulp-watch');

var mainPaths = {
    src: './src/',
    libs: './node_modules/'
};

var libs = {
    'bootstrap': 'bootstrap/dist/',
    'datatables': 'datatables.net/',
    'datatables_bootstrap': 'datatables-bootstrap3-plugin/',
    'jquery': 'jquery/dist/',
    'global': 'BluesoftBundle/Resources/assets/'
};

var styles = {
    'bootstrap': 'css/*.min.css',
    'global': 'scss/*.scss'
};

var scripts = {
   'bootstrap': 'js/*.min.js',
    'jquery': 'jquery.js',
    'datatables': 'js/*.js',
    'datatables_bootstrap': 'media/js/*.js',
    'global': 'js/*.js'
};

var fonts = {
    'bootstrap': 'fonts/*'
};

var dest = {
    'main': './web/assets/',
    'scripts': 'js',
    'styles': 'css',
    'fonts': 'fonts'
};

gulp.task('scripts_libs', function () {
    var paths = mainPaths.libs;
    var paths_src = mainPaths.src;

    return watch(mainPaths.src + '**/*.js', function () {
        console.log((new Date().toString()) + ' :: Recompiled scripts');
        gulp.src([
            paths + libs.jquery + scripts.jquery,
            paths + libs.bootstrap + scripts.bootstrap,
            // paths + libs.datatables + scripts.datatables,
            // paths + libs.datatables_bootstrap + scripts.datatables_bootstrap,
            paths_src + libs.global + scripts.global
        ])
            .pipe(uglify())
            .pipe(concat('scripts.js'))
            .pipe(gulp.dest(dest.main + dest.scripts))
    });
});


gulp.task('styles_libs', function () {
    var paths_libs = mainPaths.libs;
    var paths_src = mainPaths.src;

    return watch(mainPaths.src + '**/*.scss', function () {
        gulp.src([
            paths_libs + libs.bootstrap + styles.bootstrap,
            paths_src + libs.global + styles.global

        ])
            .pipe(sass().on('error', sass.logError))
            .pipe(concat('styles.css'))
            .pipe(gulp.dest(dest.main + dest.styles));

        console.log((new Date().toString()) + ' :: Recompiled files');
    });


});

gulp.task('fonts_libs', function () {
    var paths = mainPaths.libs;

    return gulp.src([
        paths + libs.bootstrap + fonts.bootstrap
    ])
        .pipe(gulp.dest(dest.main + dest['fonts']))
});


gulp.task('default', ['scripts_libs', 'styles_libs', 'fonts_libs']);