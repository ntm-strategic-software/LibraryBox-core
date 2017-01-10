module.exports = grunt => {

    grunt.initConfig({

        php: {
            dist: {
                options: {
                    base: './LibraryBox-landingpage/www_content',
                    keepalive: true,
                    port: 5000
                }
            }
        }

    });

    grunt.loadNpmTasks('grunt-php');

    grunt.registerTask('default', ['php']);

}