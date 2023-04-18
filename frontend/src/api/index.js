import axios from "axios";
import { createInstance } from "./apiFactory";

const WebSocketConnectUrl = process.env.REACT_APP_WEB_SOCKET_URL;
const getWebSocketChannel = (tokenId) => `private-channel.${tokenId}`;

const baseUrl = process.env.REACT_APP_API_BASE_URL;
const loginUrl = `${baseUrl}/login`;

const api = createInstance(axios, baseUrl, loginUrl);

const logIn = async ({ login, password }) => {
  const response = await api.post("/login", {
    login,
    password,
  });
  return response.data.token;
};

// According to MDN the sendBeacon method is more preferable solution to send unblocking request
// https://developer.mozilla.org/en-US/docs/Web/API/Navigator/sendBeacon#description
const logOut = () => navigator.sendBeacon(`${baseUrl}/logout`, {});

const submitBulkEnvelope = async (request) => {
  const response = await api.post("/bulk-envelope-sending", request);
  return response.data;
};

const getEquipmentAndSoftware = async () => {
  const response = await api.get("/equipments-and-software");
  return response.data;
};

const submitPermissionProfiles = async (request) => {
  const response = await api.post("/permission-profile", request);
  return response.data;
};

const getTokenId = async () => {
  const response = await api.get("/token");
  return response.data.tokenId;
};

const exportUsers = async () => {
  const response = await api.get("/users/download");
  return response.data.tokenId;
};

const getUsers = async () => {
  const response = await api.get("/users");
  return response.data.data;
};

const getProfiles = async () => {
  const response = await api.get("/permission-profile");
  return response.data.data;
};

const getAlerts = async () => {
  const response = await api.get("/monitor-alerts");
  return response.data;
};

const exportDashboard = async (fileName) => {
  const response = await api.get("/users/download");
  const content = response.data;

  // Create blob link to download
  const url = window.URL.createObjectURL(
    new Blob([content], { type: "text/csv" })
  );
  const link = document.createElement("a");
  link.href = url;
  link.setAttribute("download", `${fileName}.csv`);

  // Append to html link element page
  document.body.appendChild(link);

  // Start download
  link.click();

  // Clean up and remove the link
  link.parentNode.removeChild(link);
};

export {
  logIn,
  logOut,
  getTokenId,
  getEquipmentAndSoftware,
  submitBulkEnvelope,
  getUsers,
  getProfiles,
  getAlerts,
  submitPermissionProfiles,
  exportDashboard,
  WebSocketConnectUrl,
  getWebSocketChannel,
  exportUsers,
};
