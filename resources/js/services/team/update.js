import Vue from 'vue';

const success = (response, resolve) => {
  resolve(response);
};

const failed = (error, reject) => {
  reject(error);
};

export default (hash_id,team) => (
  new Promise((resolve, reject) => {
   Vue.$http.put(`/teams/${hash_id}`,team)
    .then((response) => {
      success(response, resolve);
    })
    .catch((error) => {
      failed(error, reject);
    });
  })
);