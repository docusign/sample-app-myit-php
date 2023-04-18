const TOKEN_KEY = "token_key";

const setAuthToken = (token) => localStorage.setItem(TOKEN_KEY, token);
const getAuthToken = () => localStorage.getItem(TOKEN_KEY);
const cleanAuthToken = () => localStorage.removeItem(TOKEN_KEY);

export { setAuthToken, getAuthToken, cleanAuthToken };
