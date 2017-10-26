'use strict';

module.exports = function (grunt) {
    // Time how long tasks take. Can help when optimizing build times
    require('time-grunt')(grunt);

    // Automatically load required grunt tasks
    require('jit-grunt')(grunt, {
        lockfile: 'grunt-lock'
    });

    // Configurable paths
    var config = {
        resources_dir: 'Resources',
        public_dir: 'Resources/public',
        dev_dir: 'Resources/public/dev',
    };

    // Define the configuration for all the tasks
    grunt.initConfig({
        // Project settings
        config: config,

        // Prevent multiple grunt instances
        lockfile: {
            grunt: {
                path: 'grunt.lock'
            }
        },

        // Watches files for changes and runs tasks based on the changed files
        watch: {
            gruntfile: {
                files: ['Gruntfile.js'],
                options: {
                    reload: true
                }
            },
            sass: {
                files: ['<%= config.resources_dir %>/sass/{,*/}*.{scss,sass}'],
                tasks: ['sass', 'postcss']
            }
        },

        // Compiles es6 js files to supported js
        browserify: {
            options: {
                watch: true,
                browserifyOptions: {
                    debug: true
                },
                transform: [
                    ['babelify', { presets: ['es2015', 'stage-0'] }]
                ]
            },
            dist: {
                files: {
                    '<%= config.dev_dir %>/js/app.js': ['<%= config.resources_dir %>/es6/app.js']
                }
            }
        },

        // Compiles Sass to CSS and generates necessary files if requested
        sass: {
            options: {
                sourceMap: true,
                sourceMapEmbed: true,
                sourceMapContents: true,
                includePaths: ['.']
            },
            dist: {
                files: [{
                    expand: true,
                    cwd: '<%= config.resources_dir %>/sass',
                    src: ['*.{scss,sass}'],
                    dest: '.tmp/css',
                    ext: '.css'
                }]
            }
        },

        postcss: {
            options: {
                map: true,
                processors: [
                    // Add vendor prefixed styles
                    require('autoprefixer')({
                        browsers: ['> 1%', 'last 2 versions', 'Firefox ESR', 'Opera 12.1']
                    })
                ]
            },
            dist: {
                files: [{
                    expand: true,
                    cwd: '.tmp/css/',
                    src: '{,*/}*.css',
                    dest: '<%= config.dev_dir %>/css'
                }]
            }
        },

        cssmin: {
            target: {
                options: {
                    level: 1
                },
                files: [{
                    expand: true,
                    cwd: '<%= config.dev_dir %>/css',
                    src: ['*.css', '!*.min.css'],
                    dest: '<%= config.public_dir %>/css',
                    ext: '.css'
                }]
            }
        },

        uglify: {
            my_target: {
                files: {
                    '<%= config.public_dir %>/js/app.js': ['<%= config.dev_dir %>/js/app.js']
                }
            }
        }
    });


    grunt.registerTask('serve', 'Start the server and preview your app', function () {
        grunt.task.run([
            'lockfile',
            'sass:dist',
            'postcss',
            'browserify',
            'watch'
        ]);
    });

    grunt.registerTask('default', [
        'serve'
    ]);

    grunt.registerTask('build', 'Build production js', function () {
        grunt.task.run([
            'browserify',
            'sass:dist',
            'postcss',
            'cssmin',
            'uglify'
        ]);
    });
};
