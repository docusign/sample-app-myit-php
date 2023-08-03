import React, { useEffect } from "react";
import { useTranslation } from "react-i18next";
import parse from "html-react-parser";
import { useSelector, useDispatch } from "react-redux";
import { RequestForm } from "./components/RequestForm";
import { ApiDescription, Loader } from "../../components";
import { exportDashboardData, loadAlertsData } from "../../state/appSlice";

import {
  LOADING_STATE_PENDING,
  LOADING_STATE_IDLE,
} from "../../state/constants";

export const EmployeeMonitoring = () => {
  const dispatch = useDispatch();
  const appState = useSelector((state) => state.app);

  useEffect(() => {
    if (appState.loading === LOADING_STATE_IDLE) {
      dispatch(loadAlertsData());
    }
  }, [appState]);

  const { t } = useTranslation("EmployeeMonitoring");

  const handleExport = () =>
    dispatch(exportDashboardData(t("Dashboard.ExportFileName")));

  return (
    <section className="employee-monitoring-page">
      <Loader show={appState.loading === LOADING_STATE_PENDING} />
      <div className="container">
        <div className="row">
          <RequestForm
            users={appState.users}
            alerts={appState.alerts}
            profiles={appState.profiles}
            software={appState.software}
            equipment={appState.equipment}
            onExport={handleExport}
          />
          <ApiDescription description={parse(t("ApiDecription"))} />
        </div>
      </div>
    </section>
  );
};
