/* eslint-disable no-param-reassign */
import { getAuthToken } from "../services/localRepository";

export const createInstance = (axios, baseUrl, loginUrl) => {
  const api = axios.create({
    baseURL: baseUrl,
    withCredentials: true,
  });

  // Request interceptor for API calls
  api.interceptors.request.use(
    async (config) => {
      config.headers = {
        Accept: "application/json",
        "Content-Type": "application/json",
      };
      if (config.url !== loginUrl) {
        const accessToken = getAuthToken();
        config.headers.Authorization = `Bearer ${accessToken}`;
      }
      return config;
    },
    (error) => {
      Promise.reject(error);
    }
  );

  api.interceptors.response.use(
    (response) => response,
    (error) => {
      // eslint-disable-next-line no-console
      console.error(`API call failed. Error:  ${error}`);
      return Promise.reject(error);
    }
  );

  return api;
};
