var gulp = require('gulp');
var package = require('./package.json');
var $ = require('gulp-load-plugins')();

gulp.task('sass', function () {
    return gulp.src('./assets/src/scss/main.scss')
        .pipe($.rename('plugin-state-switcher.min.css'))
        .pipe($.sourcemaps.init())
        .pipe($.sass()
            .on('error', $.sass.logError))
        .pipe($.sourcemaps.init())
        .pipe($.autoprefixer({
            browsers: ['last 2 versions', 'ie >= 9']
        }))
        .pipe($.sass({outputStyle: 'compressed'}))
        .pipe($.sourcemaps.write())
        .pipe(gulp.dest('./assets/dist/css/'))
        .pipe($.notify({message: 'SASS complete'}));
});

gulp.task('scripts', function () {
    return gulp.src('./assets/src/js/*.js')
        .pipe($.concat('plugin-state-switcher.min.js'))
        .pipe(gulp.dest('./assets/dist/js/'))
        .pipe($.sourcemaps.init())
        .pipe($.uglify())
        .pipe($.sourcemaps.write())
        .pipe(gulp.dest('./assets/dist/js/'))
        .pipe($.notify({message: 'JS complete'}));
});

gulp.task('version', function () {
    return gulp.src(['**/*.{php,js,scss,txt}', '!node_modules/'], {base: './'})
        .pipe($.justReplace([
            {
                search: /\{\{VERSION}}/g,
                replacement: package.version
            },
            {
                search: /(\* Version: )\d\.\d\.\d/,
                replacement: "$1" + package.version
            }, {
                search: /(define\( 'PLUGINSS_VERSION', ')\d\.\d\.\d/,
                replacement: "$1" + package.version
            }, {
                search: /(Stable tag: )\d\.\d\.\d/,
                replacement: "$1" + package.version
            }
        ]))
        .pipe(gulp.dest('./'));
});

gulp.task('generate_pot', function () {
    return gulp.src('./**/*.php')
        .pipe($.sort())
        .pipe($.wpPot({
            domain: 'learndash-gradebook',
            destFile: 'learndash-gradebook.pot',
            package: 'LearnDash_Gradebook',
        }))
        .pipe(gulp.dest('./languages/'));
});

gulp.task('default', ['sass', 'scripts'], function () {
    gulp.watch(['./assets/src/scss/*.scss'], ['sass']);
    gulp.watch(['./assets/src/js/*.js'], ['scripts']);
});

gulp.task('build', ['version', 'generate_pot']);
