import axios from 'axios';
import authHeader from './auth-header';

const API_URL = 'http://localhost/api/';

class UserService {
  update(user) {
    return axios.put(API_URL + 'update/user', {
      name: user.name,
      email: user.email,
      password: user.password,
      repeat_password: user.repeat_password,
    }, { headers: authHeader() }).then(response => {
      let res = response.data;
      if (res.data.access_token) {
        localStorage.setItem('user', JSON.stringify(res.data));
      }

      return res.data;
    });
  }
}

export default new UserService();