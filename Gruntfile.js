module.exports = function (grunt) {
  grunt.registerTask('default', function(){
    console.log('');
    grunt.log.error('*************************************************');
    grunt.log.error('Grunt needs to be started from one of the bundles');
    grunt.log.error('*************************************************');
  });
};
