import React from "react";
import PropTypes from "prop-types";
import { Card } from "react-bootstrap";
import { useTranslation } from "react-i18next";
import { 
  BsPersonFillUp, 
  BsFillEnvelopePaperHeartFill,
  BsFillEnvelopePlusFill,
  BsFillEnvelopeSlashFill,
  BsFillEnvelopeXFill,
} from "react-icons/bs";
import {
  EVENT_USER_UPDATED,
  EVENT_ENVELOPE_SENT,
  EVENT_ENVELOPE_SIGNED,
  EVENT_ENVELOPE_VOIDED,
  EVENT_ENVELOPE_DECLINED,
} from "../../../state/constants";

export const AlertList = ({ alerts }) => {
  const { t } = useTranslation("EmployeeMonitoring");

  const mapAlertTypeToImg = (alertType) => {
    switch (alertType) {
      case EVENT_USER_UPDATED:
        return <BsPersonFillUp/>;
      case EVENT_ENVELOPE_SIGNED:
        return <BsFillEnvelopePaperHeartFill/>;
      case EVENT_ENVELOPE_SENT:
        return <BsFillEnvelopePlusFill/>;
      case EVENT_ENVELOPE_VOIDED:
        return <BsFillEnvelopeSlashFill/>;
        case EVENT_ENVELOPE_DECLINED:
          return <BsFillEnvelopeXFill/>;
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
              }).format(new Date(alert.createdAt))}
            </div>
            <div className="col-lg-1 event-icons">
              {mapAlertTypeToImg(alert.event)}
            </div>
            <div className="col-lg-4">{t(alert.localizationKey)}</div>
            <div className="col-lg-4">{alert.user}</div>
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
      event: PropTypes.string.isRequired,
      localizationKey: PropTypes.string.isRequired,
      createdAt: PropTypes.string.isRequired,
    })
  ),
};

AlertList.defaultProps = {
  alerts: [],
};
