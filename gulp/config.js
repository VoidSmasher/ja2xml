var path = require('path');

module.exports = {
    src_path : path.join(__dirname, '..', 'htsource'),
	trg_path : path.join(__dirname, '..', 'htdocs/assets'),
	tasks : {
		css : {
			public : {
				'styles' : null
			},
			mobile : {
				'styles' : null
			}
		},
		js : {
			public : {
				'main' : null
			},
			mobile : {
				'main' : null
			}
		},
		svg : {
			common : {
				'sprite' : null
			}
		}
	},
	browser_sync : {
		proxy: {
			target: "new-project.local"
		},
		open: false
	}
};