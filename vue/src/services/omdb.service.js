import axios from 'axios';
import authHeader from './auth-header';

const API_URL = 'http://localhost/api/';

class OmdbService {
  getData(search, page) {
    return axios.get(API_URL + 'movies', { 
      headers: authHeader(),
      params: {
        search: search,
        page: page,
      }
    });
  } 

  getRateMovie(rating, id) {
    return axios.post(API_URL + 'rate/movie', {
        imdb_id: id,
        rate: rating,
      }, { headers: authHeader() });
  }

  downloadMovie(code = null) {
    return axios.get(API_URL + 'download/movie?code=' + code + '&v=' + Math.floor(Math.random() * 1000), {
      responseType: 'blob',
      headers: authHeader(),
    });
  }

  sendCode() {
    return axios.get(API_URL + 'send/code', { headers: authHeader() });
  }
}

export default new OmdbService();