'use strict';

module.exports = function (grunt) {
    // Time how long tasks take. Can help when optimizing build times
    require('time-grunt')(grunt);

    require('jit-grunt')(grunt, {
        lockfile: 'grunt-lock',
    });

    const collapse = require('bundle-collapser/plugin');

    // Configurable paths
    var config = {
        dev: 'Resources/public/dev',
        dist: 'Resources/public',
        resources: 'Resources'
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
                files: ['<%= config.resources %>/sass/{,*/}*.{scss,sass}'],
                tasks: ['sass:dev', 'postcss:dev'],
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
                    '<%= config.dev %>/js/app.js': ['<%= config.resources %>/es6/app.js'],
                },
            },

            dist: {
                options: {
                    transform: [
                        ['babelify', { presets: ['env', 'es2015', 'stage-0'] }],
                        ['uglifyify'],
                    ],
                    plugin: [collapse],
                },
                files: {
                    '<%= config.dist %>/js/app.js': ['<%= config.resources %>/es6/app.js'],
                },
            },
        },

        // Compiles Sass to CSS and generates necessary files if requested
        sass: {
            options: {
                implementation: require('node-sass'),
                includePaths: ['.'],
            },

            dev: {
                options: {
                    sourceMap: true,
                    sourceMapEmbed: true,
                    sourceMapContents: true
                },
                files: [{
                    expand: true,
                    cwd: '<%= config.resources %>/sass',
                    src: ['*.{scss,sass}'],
                    dest: '.tmp/css',
                    ext: '.css',
                }],
            },

            dist: {
                options: {
                    sourceMap: false,
                    sourceMapEmbed: false,
                    sourceMapContents: false,
                    outputStyle: 'compressed'
                },
                files: [{
                    expand: true,
                    cwd: '<%= config.resources %>/sass',
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

            dev: {
                files: [{
                    expand: true,
                    cwd: '.tmp/css/',
                    src: '{,*/}*.css',
                    dest: '<%= config.dev %>/css',
                }],
            },

            dist: {
                files: [{
                    expand: true,
                    cwd: '.tmp/css/',
                    src: '{,*/}*.css',
                    dest: '<%= config.dist %>/css',
                }],
            },
        },

        uglify: {
            dist: {
                files: {
                    '<%= config.dist %>/js/app.js': ['<%= config.dist %>/js/app.js'],
                },
            },
        },

        clean: {
            dev: '<%= config.dev %>',
        },

        shell: {
            dev: {
                command: [
                    'ln -s ../fonts <%= config.dev %>',
                    'ln -s ../images <%= config.dev %>',
                ].join('\n'),
            },
        },
    });

    grunt.registerTask('server', function () {
        grunt.task.run([
            'fast_build',
            'watch',
        ]);
    });

    grunt.registerTask('fast_build', function () {
        grunt.task.run([
            'clean:dev',
            'lockfile',
            'sass:dev',
            'postcss:dev',
            'browserify:dev',
            'shell:dev',
        ]);
    });

    grunt.registerTask('build', function () {
        grunt.task.run([
            'sass:dist',
            'postcss:dist',
            'browserify:dist',
            'uglify:dist',
        ]);
    });

    grunt.registerTask('default', ['server']);
};
