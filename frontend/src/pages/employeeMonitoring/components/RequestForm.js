import React from "react";
import PropTypes from "prop-types";
import { useTranslation } from "react-i18next";
import { Dashboard, AlertList } from "./index";

export const RequestForm = ({
  users,
  alerts,
  profiles,
  software,
  equipment,
  onExport,
}) => {
  const { t } = useTranslation("EmployeeMonitoring");

  return (
    <div className="request-form-card col-lg-8">
      <h1 className="mb-4">{t("Title")}</h1>
      <AlertList alerts={alerts} />
      <Dashboard
        users={users}
        profiles={profiles}
        software={software}
        equipment={equipment}
        onExport={onExport}
      />
    </div>
  );
};

RequestForm.propTypes = {
  users: PropTypes.arrayOf(
    PropTypes.shape({
      id: PropTypes.number.isRequired,
      name: PropTypes.string.isRequired,
      email: PropTypes.string.isRequired,
    })
  ),
  alerts: PropTypes.arrayOf(
    PropTypes.shape({
      id: PropTypes.string.isRequired,
      user: PropTypes.string.isRequired,
      event: PropTypes.string.isRequired,
      localizationKey: PropTypes.string.isRequired,
      createdAt: PropTypes.string.isRequired,
    })
  ),
  profiles: PropTypes.arrayOf(
    PropTypes.shape({
      label: PropTypes.string.isRequired,
      value: PropTypes.number.isRequired,
    })
  ),
  software: PropTypes.arrayOf(
    PropTypes.shape({
      label: PropTypes.string.isRequired,
      value: PropTypes.number.isRequired,
    })
  ),
  equipment: PropTypes.arrayOf(
    PropTypes.shape({
      label: PropTypes.string.isRequired,
      value: PropTypes.number.isRequired,
    })
  ),
  onExport: PropTypes.func.isRequired,
};

RequestForm.defaultProps = {
  users: [],
  alerts: [],
  profiles: [],
  software: [],
  equipment: [],
};
