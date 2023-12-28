  
export const getUploadUrl = state => state.uploader.file.url;
export const getUpload = state => state.uploader.file;
export const getUploadPath = state => state.uploader.file.path;
export const getImportObject = state => state.uploader.importObject;