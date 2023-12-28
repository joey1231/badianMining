import Vue from 'vue';
import helpers from './../../utils/helper.js';
const success = (response, resolve) => {
  resolve(response);
};

const failed = (error, reject) => {
  reject(error);
};

export default (params, hash_id)=> (
  new Promise((resolve, reject) => {

    Vue.$http.get(helpers.buildUrl(`/adviser-view/records/search/${hash_id}`, params))
    .then((response) => {
      success(response, resolve);
    })
    .catch((error) => {
      failed(error, reject);
    });
  })
);