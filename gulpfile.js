var gulp = require('gulp'),
    browserSync = require('browser-sync').create(),
    sass = require('gulp-sass');

browserSync.init({
    injectChanges: true,
    proxy: "http://internsiteb.lndo.site:8000"
});

gulp.task('sass', function () {
    gulp.watch('web/themes/drupal8_zymphonies_theme/sass/*.scss', function () {
        return gulp.src("web/themes/drupal8_zymphonies_theme/sass/**/*.scss")
            .pipe(sass())
            .pipe(gulp.dest('web/themes/drupal8_zymphonies_theme/css'))
            .pipe(browserSync.stream());
    });
});

gulp.task('watch', function () {
    gulp.watch('web/themes/drupal8_zymphonies_theme/sass/**/*.scss', gulp.series('sass'));
    gulp.watch(['web/themes/drupal8_zymphonies_theme/css/*.css', 'web/themes/drupal8_zymphonies_theme/templates/**/*.html.twig', 'web/themes/drupal8_zymphonies_theme/js/*.js']).on('change', browserSync.reload);
})

gulp.task('default', gulp.series('watch'));

