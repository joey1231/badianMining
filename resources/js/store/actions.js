import * as types from './types.js';

export const setUploadFile = ({commit}, file) => {
	commit(types.SET_UPLOAD_FILE, file);
}

export const setUploadFilePath = ({commit}, path) => {
	commit(types.SET_UPLOAD_FILE_PATH, path);
}

export const setImportObject = ({commit}, importObject) => {
	commit(types.SET_IMPORT_OBJECT, importObject);
}
