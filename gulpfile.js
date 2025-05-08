import gulpSass from "gulp-sass";
import * as nodeSass from 'sass'
import gulp from "gulp";
import del from "del";

const sass = gulpSass(nodeSass);

gulp.task('styles', () => {
    return gulp.src('assets/style/main.scss')
        .pipe(sass().on('error', sass.logError))
        .pipe(gulp.dest('./assets/style/'));
});

gulp.task('clean', () => {
    return del([
        '/main.css',
    ]);
});

gulp.task('default', gulp.series(['clean', 'styles']));