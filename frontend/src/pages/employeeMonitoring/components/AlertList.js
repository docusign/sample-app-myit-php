import React from "react";
import PropTypes from "prop-types";
import { Card } from "react-bootstrap";
import { useTranslation } from "react-i18next";
import {
  ALERT_TYPE_SUCCESS,
  ALERT_TYPE_WARNING,
  ALERT_TYPE_DANGER,
} from "../../../state/constants";
import alertSuccess from "../../../assets/img/alert-success.png";
import alertWarning from "../../../assets/img/alert-warning.png";
import alertDanger from "../../../assets/img/alert-danger.png";

export const AlertList = ({ alerts }) => {
  const { t } = useTranslation("EmployeeMonitoring");

  const mapAlertTypeToImg = (alertType) => {
    switch (alertType) {
      case ALERT_TYPE_SUCCESS:
        return alertSuccess;
      case ALERT_TYPE_WARNING:
        return alertWarning;
      case ALERT_TYPE_DANGER:
        return alertDanger;
      default:
        console.log(`Error type '${alertType}' is not defined`);
        return null;
    }
  };

  return (
    <Card className="alerts-card">
      <Card.Header className="alerts-card-header">
        <h2 className="">{t("AlertList.Title")}</h2>
      </Card.Header>
      <Card.Body className="alerts-card-body">
        {alerts.map((alert) => (
          <div className="alert-wrapper row" key={alert.id}>
            <div className="col-lg-3">
              {new Intl.DateTimeFormat("en-US", {
                dateStyle: "short",
                timeStyle: "medium",
              }).format(new Date(alert.timestamp))}
            </div>
            <div className="col-lg-1">
              <img src={mapAlertTypeToImg(alert.type)} alt="" />
            </div>
            <div className="col-lg-4">{alert.user}</div>
            <div className="col-lg-4">{t(alert.localizationKey)}</div>
          </div>
        ))}
      </Card.Body>
    </Card>
  );
};

AlertList.propTypes = {
  alerts: PropTypes.arrayOf(
    PropTypes.shape({
      id: PropTypes.string.isRequired,
      user: PropTypes.string.isRequired,
      type: PropTypes.string.isRequired,
      localizationKey: PropTypes.string.isRequired,
      timestamp: PropTypes.string.isRequired,
    })
  ),
};

AlertList.defaultProps = {
  alerts: [],
};
