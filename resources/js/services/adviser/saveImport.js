import Vue from 'vue';

const success = (response, resolve) => {
  resolve(response);
};

const failed = (error, reject) => {
  reject(error);
};

export default model => (
  new Promise((resolve, reject) => {
   Vue.$http.post(`/advisers/save-csv`,model)
    .then((response) => {
      success(response, resolve);
    })
    .catch((error) => {
      failed(error, reject);
    });
  })
);