module.exports = grunt => {

    grunt.initConfig({

        php: {
            dist: {
                options: {
                    base: './temp',
                    keepalive: true,
                    port: 5000
                        // open: true
                }
            }
        },

        copy: {
            lib: {
                files: [ //{
                    // expand: true,
                    // cwd: './LibraryBox-landingpage/www_content/',
                    // src: '**',
                    // dest: './temp/content/',
                    // filter: 'isFile'
                    //},
                    // {
                    //     expand: true,
                    //     cwd: './LibraryBox-landingpage/www_content/',
                    //     src: '*.php',
                    //     dest: './temp/content/',
                    //     filter: 'isFile'
                    // }, {
                    //     expand: true,
                    //     cwd: './LibraryBox-landingpage/www_content/',
                    //     src: '*.html',
                    //     dest: './temp/content/',
                    //     filter: 'isFile'
                    // },
                    {
                        expand: true,
                        cwd: './LibraryBox-landingpage/www_content/',
                        src: '**',
                        dest: './temp/content/',
                    },
                    {
                        expand: true,
                        cwd: './customization/www_librarybox/',
                        src: '**',
                        dest: './temp/content/www_librarybox/',
                    },
                    // { expand: true, cwd: './LibraryBox-landingpage/www_content/css', src: '**', dest: './temp/css', filter: 'isFile' },
                    // { expand: true, cwd: './LibraryBox-landingpage/www_content/dir-images', src: '**', dest: './temp/dir-images', filter: 'isFile' },
                    // { expand: true, cwd: './LibraryBox-landingpage/www_content/fonts', src: '**', dest: './temp/fonts', filter: 'isFile' },
                    // { expand: true, cwd: './LibraryBox-landingpage/www_content/img', src: '**', dest: './temp/img', filter: 'isFile' },
                    // { expand: true, cwd: './LibraryBox-landingpage/www_content/js', src: '**', dest: './temp/js', filter: 'isFile' },
                    // { expand: true, cwd: './LibraryBox-landingpage/www_content/locales', src: '**', dest: './temp/locales', filter: 'isFile' }
                ]
            },
            main: {
                cwd: './',
                src: 'redirector.html',
                dest: 'temp/index.html',
                options: {}
            }
        },

        watch: {
            scripts: {
                files: [
                    './LibraryBox-landingpage/www_content/**',
                    './customization/www_librarybox/**'
                ],
                tasks: ['copy'],
                options: {
                    spawn: false
                }
            }
        }

    });

    grunt.loadNpmTasks('grunt-php');
    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.loadNpmTasks('grunt-contrib-copy');

    grunt.registerTask('default', ['php']);

}