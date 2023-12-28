import {
 SET_UPLOAD_FILE, SET_UPLOAD_FILE_PATH, SET_IMPORT_OBJECT
} from './../types.js';

export default {
	[SET_UPLOAD_FILE](state, file){
		state.file = file;
	},
	[SET_UPLOAD_FILE_PATH](state, path){
		state.file.path = path;
	},

	[SET_IMPORT_OBJECT](state, importObject){
		state.importObject = importObject;
	},

}