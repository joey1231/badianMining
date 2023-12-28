import Vue from 'vue';
const success = (response, resolve) => {
  resolve(response);
};

const failed = (error, reject) => {
  reject(error);
};

export default (hash_id,data)=> (
  new Promise((resolve, reject) => {

    Vue.$http.post(`/advisers/change-access/${hash_id}`,data)
    .then((response) => {
      success(response, resolve);
    })
    .catch((error) => {
      failed(error, reject);
    });
  })
);