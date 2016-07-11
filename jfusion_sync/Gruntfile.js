module.exports = function(grunt) {
  require('jit-grunt')(grunt);

  grunt.initConfig({
       
    synchard: {
        remotedest: {
            options: {
                ssh: true,
                privateKey: "/home/mrfg/.ssh/Area51.pem"
            },
            files: {
                'ubuntu@area51.ukriversguidebook.co.uk:/var/www/ukrgb/phpbb/ext/jfusion': ['jfusion/']
            }
        }
    },

    watch: {
      styles: {
        files: ['**/*'], // which files to watch
        tasks: ['synchard'],
        options: {
          nospawn: true
        }
      }
    }
  });
  
  grunt.file.setBase('../../org.jfusion.jfusion/components/com_jfusion/plugins/phpbb31/')
  
  grunt.registerTask('sync', ['synchard']);

  grunt.registerTask('default', ['watch']);
};