'use strict';

module.exports = function (grunt) {
    // Time how long tasks take. Can help when optimizing build times
    require('time-grunt')(grunt);

    // Automatically load required grunt tasks
    require('jit-grunt')(grunt, {
        lockfile: 'grunt-lock',
    });

    const collapse = require('bundle-collapser/plugin');

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
                path: 'grunt.lock',
            },
        },

        // Watches files for changes and runs tasks based on the changed files
        watch: {
            gruntfile: {
                files: ['Gruntfile.js'],
                options: {
                    reload: true,
                },
            },
            sass: {
                files: ['<%= config.resources_dir %>/sass/{,*/}*.{scss,sass}'],
                tasks: ['sass', 'postcss'],
            },
        },

        // Compiles es6 js files to supported js
        browserify: {
            dev: {
                options: {
                    watch: true,
                    browserifyOptions: {
                        debug: true,
                    },
                    transform: [
                        ['babelify', { presets: ['env', 'es2015', 'stage-0'] }],
                    ],
                },
                files: {
                    '<%= config.dev_dir %>/js/app.js': ['<%= config.resources_dir %>/es6/app.js'],
                },
            },
            prod: {
                options: {
                    transform: [
                        ['babelify', { presets: ['env', 'es2015', 'stage-0'] }],
                        ['uglifyify'],
                    ],
                    plugin: [collapse],
                },
                files: {
                    '<%= config.public_dir %>/js/app.js': ['<%= config.resources_dir %>/es6/app.js'],
                },
            },
        },

        // Compiles Sass to CSS and generates necessary files if requested
        sass: {
            options: {
                sourceMap: true,
                sourceMapEmbed: true,
                sourceMapContents: true,
                includePaths: ['.'],
            },
            dist: {
                files: [{
                    expand: true,
                    cwd: '<%= config.resources_dir %>/sass',
                    src: ['*.{scss,sass}'],
                    dest: '.tmp/css',
                    ext: '.css',
                }],
            },
        },

        postcss: {
            options: {
                map: true,
                processors: [
                    // Add vendor prefixed styles
                    require('autoprefixer')({
                        browsers: ['> 1%', 'last 3 versions', 'Firefox ESR', 'Opera 12.1'],
                    }),
                ],
            },
            dist: {
                files: [{
                    expand: true,
                    cwd: '.tmp/css/',
                    src: '{,*/}*.css',
                    dest: '<%= config.public_dir %>/css',
                }],
            },
        },

        uglify: {
            my_target: {
                files: {
                    '<%= config.public_dir %>/js/app.js': ['<%= config.public_dir %>/js/app.js'],
                },
            },
        },
    });

    grunt.registerTask('serve', 'Start the server and preview your app', function () {
        grunt.task.run([
            'lockfile',
            'sass:dist',
            'postcss',
            'browserify:dev',
            'watch',
        ]);
    });

    grunt.registerTask('default', [
        'serve',
    ]);

    grunt.registerTask('build', 'Build production assets', function () {
        grunt.task.run([
            'lockfile',
            'sass:dist',
            'postcss',
            'browserify:prod',
            'uglify',
        ]);
    });
};
