import React, { Suspense, useEffect } from "react";
import { Routes, Route } from "react-router-dom";
import { useDispatch, useSelector } from "react-redux";
import { useTranslation } from "react-i18next";
import "react-toastify/dist/ReactToastify.css";
import "./assets/scss/main.scss";
import { usePrompt } from "./hooks/usePrompt";

import {
  logIn,
  getTokenId,
  logOut,
  WebSocketConnectUrl,
  getWebSocketChannel,
} from "./api";

import {
  loadIntitialData,
  setPendingLoading,
  rollbackUsersChanges,
  addAlert,
} from "./state/appSlice";

import {
  setAuthToken,
  cleanAuthToken,
  getAuthToken,
} from "./services/localRepository";

import { EquipmentAndSoftwareApproval } from "./pages/equipmentAndSoftwareApproval";
import { BulkEmployeePermissionProfileModification } from "./pages/bulkEmployeePermissionProfileModification";
import { EmployeeMonitoring } from "./pages/employeeMonitoring";

import { Layout } from "./components/Layout";
import { Home } from "./pages/home";
import { useWebSocket } from "./hooks/useWebSocket";

const App = () => {
  const dispatch = useDispatch();
  const { t } = useTranslation("Common");

  const [connect, disconnect] = useWebSocket(WebSocketConnectUrl);
  const appState = useSelector((state) => state.app);
  usePrompt(
    t("UnsavedChangesMessage"),
    () => dispatch(rollbackUsersChanges()),
    appState.users.some((u) => u.isDirty)
  );

  const handleLogIn = async () => {
    let token = getAuthToken();
    if (!token) {
      dispatch(setPendingLoading());
      token = await logIn({
        login: process.env.REACT_APP_MANAGER_LOGIN,
        password: process.env.REACT_APP_MANAGER_PASSWORD,
      });
      setAuthToken(token);
      const tokenId = await getTokenId();
      connect(getWebSocketChannel(tokenId), (data) => {
        dispatch(addAlert(data));
      });

      dispatch(loadIntitialData());
    }
  };

  const handleLogOut = () => {
    cleanAuthToken();
    disconnect();
    logOut();
  };

  const setupVisibilityChangeListener = () => {
    window.addEventListener("beforeunload", () => {
      handleLogOut();
    });
  };

  useEffect(() => {
    setupVisibilityChangeListener();
    handleLogIn();
  }, []);

  const routes = (
    <Routes>
      <Route path="/" element={<Home />} />
      <Route
        path="/equipment_and_software_approval"
        element={<EquipmentAndSoftwareApproval />}
        on
      />
      <Route
        path="/bulk_employee_permission_profile_modification"
        element={<BulkEmployeePermissionProfileModification />}
      />
      <Route path="/employee_monitoring" element={<EmployeeMonitoring />} />
    </Routes>
  );

  return (
    <Suspense fallback="">
      <Layout>{routes}</Layout>
    </Suspense>
  );
};

export default App;
