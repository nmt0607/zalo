import axios from "axios";
import { url } from "define";

axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";
axios.defaults.baseURL = process.env.APP_URL || "http://localhost";
axios.defaults.headers.common["Authorization"] = `Bearer ${localStorage.getItem(
  "token"
)}`;

export async function getAllUsers(index, count) {
  try {
    const response = await axios.post(url.getAllUsers, {
      index,
      count,
    });
    const { code, message, data, total } = response.data;

    if (code !== 1000) {
      throw { code, message };
    }

    return { users: data, total };
  } catch (error) {
    console.error("getAllUsers", error);

    return null;
  }
}

export async function getUserInfo(userId) {
  try {
    const response = await axios.post(url.getUserInfo, {
      user_id: userId,
    });
    const { code, message, data } = response.data;

    if (code !== 1000) {
      throw { code, message };
    }

    return data;
  } catch (error) {
    console.error("getUserInfo", error);

    return null;
  }
}

export async function setUserState(userId) {
  try {
    const response = await axios.post(url.setUserState, {
      user_id: userId,
    });
    const { code, message } = response.data;

    if (code !== 1000) {
      throw { code, message };
    }

    return true;
  } catch (error) {
    console.error("setUserState", error);

    return null;
  }
}
