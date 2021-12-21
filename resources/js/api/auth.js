import axios from "axios";
import { url } from 'define';

axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";
axios.defaults.baseURL = process.env.APP_URL || "http://localhost";

export async function login(phoneNumber, password) {
  try {
    const response = await axios.post(url.login, {
      phonenumber: phoneNumber,
      password,
    });
    const { code, message, data } = response.data;

    if (code !== 1000) {
      throw { code, message };
    }

    const { token } = data ;

    localStorage.setItem('token', token);
  } catch (error) {
    throw error;
  }
}
