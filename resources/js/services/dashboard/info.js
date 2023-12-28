import Vue from 'vue';
const success = (response, resolve) => {
  resolve(response);
};

const failed = (error, reject) => {
  reject(error);
};

export default ()=> (
  new Promise((resolve, reject) => {

    Vue.$http.get(`/dashboard/info`)
    .then((response) => {
      success(response, resolve);
    })
    .catch((error) => {
      failed(error, reject);
    });
  })
);