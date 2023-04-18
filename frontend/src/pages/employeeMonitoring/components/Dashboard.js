import React from "react";
import PropTypes from "prop-types";
import { Button, Card } from "react-bootstrap";
import { useTranslation } from "react-i18next";

export const Dashboard = ({
  users,
  profiles,
  software,
  equipment,
  onExport,
}) => {
  const { t } = useTranslation("EmployeeMonitoring");

  const getLabel = (options, value) =>
    value ? options.find((op) => op.value === value).label : "";

  const getLabelList = (options, values) =>
    values ? options.filter((op) => values.includes(op.value)) : [];

  return (
    <Card className="dashboard-card">
      <Card.Header className="dashboard-card-header">
        <h2 className="">{t("Dashboard.Title")}</h2>
        <Button variant="link" onClick={onExport}>
          <span className="gradient-text">{t("Dashboard.Export")}</span>
        </Button>
      </Card.Header>
      <Card.Body className="dashboard-card-body">
        {users.map((user) => (
          <div className="dashboard-user-wrapper" key={user.id}>
            <h3 className="dashboard-user-header">{user.name}</h3>
            <div className="row">
              <div className="col-lg-4">
                <div className="dashboard-user-field">
                  <div className="dashboard-user-field-label">
                    {t("Dashboard.Email")}
                  </div>
                  <div className="dashboard-user-field-value">{user.email}</div>
                </div>
                <div className="dashboard-user-field">
                  <div className="dashboard-user-field-label">
                    {t("Dashboard.PermissionProfile")}
                  </div>
                  <div className="dashboard-user-field-value">
                    {getLabel(profiles, user.profileId)}
                  </div>
                </div>
              </div>
              <div className="col-lg-4">
                <div className="dashboard-user-field">
                  <div className="dashboard-user-field-label">
                    {t("Dashboard.Software")}
                  </div>
                  <div className="dashboard-user-field-list">
                    {user.software &&
                      getLabelList(software, user.software).map((s) => (
                        <div key={s.value} className="badge bg-dark">
                          {s.label}
                        </div>
                      ))}
                  </div>
                </div>
              </div>
              <div className="col-lg-4">
                <div className="dashboard-user-field">
                  <div className="dashboard-user-field-label">
                    {t("Dashboard.Equipment")}
                  </div>
                  <div className="dashboard-user-field-list">
                    {user.equipment &&
                      getLabelList(equipment, user.equipment).map((eq) => (
                        <div key={eq.value} className="badge bg-dark">
                          {eq.label}
                        </div>
                      ))}
                  </div>
                </div>
              </div>
            </div>
          </div>
        ))}
      </Card.Body>
    </Card>
  );
};

Dashboard.propTypes = {
  users: PropTypes.arrayOf(
    PropTypes.shape({
      id: PropTypes.number.isRequired,
      name: PropTypes.string.isRequired,
      email: PropTypes.string.isRequired,
      profileId: PropTypes.number.isRequired,
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

Dashboard.defaultProps = {
  users: [],
  profiles: [],
  software: [],
  equipment: [],
};
