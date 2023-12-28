import Vue from 'vue';
import helpers from './../../utils/helper.js';
const success = (response, resolve) => {
  resolve(response);
};

const failed = (error, reject) => {
  reject(error);
};

export default (hash_id)=> (
  new Promise((resolve, reject) => {

    Vue.$http.get(helpers.buildUrl(`/adviser-view/records/supplier-link/${hash_id}`, {}))
    .then((response) => {
      success(response, resolve);
    })
    .catch((error) => {
      failed(error, reject);
    });
  })
);