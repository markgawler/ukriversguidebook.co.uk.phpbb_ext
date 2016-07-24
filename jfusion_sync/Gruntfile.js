module.exports = function(grunt) {
  require('jit-grunt')(grunt);

  grunt.initConfig({
       
    synchard: {
        remotedest: {
            options: {
            	args: ['-av'],
            	exclude: ['language'],
                ssh: true,
                privateKey: "/home/mrfg/.ssh/Area51.pem"
            },
            files: {
            	'ubuntu@area51.ukriversguidebook.co.uk:/var/www/ukrgb/phpbb/ext/jfusion': ['plugins/phpbb31/jfusion/'],
            	'ubuntu@area51.ukriversguidebook.co.uk:/var/www/ukrgb/joomla/components/com_jfusion/': [''],
                
            }
        }
    },

    watch: {
      styles: {
        files: ['plugins/phpbb31/**/*'], // which files to watch
        tasks: ['synchard'],
        options: {
          nospawn: true
        }
      }
    }
  });
  
  grunt.file.setBase('../../org.jfusion.jfusion/components/com_jfusion/')
  
  grunt.registerTask('sync', ['synchard']);

  grunt.registerTask('default', ['watch']);
};