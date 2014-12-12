module.exports = function(grunt) {
    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),
        
        watch: {
            sass: {
                options: {
                    livereload: true
                },
                files: ['web/css/*', 'app/Resources/views/auction/*']
            }
        }
    });
    
    grunt.loadNpmTasks('grunt-contrib-watch');
};