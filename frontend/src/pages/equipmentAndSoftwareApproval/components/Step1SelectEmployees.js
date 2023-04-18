import React from "react";
import PropTypes from "prop-types";
import { useTranslation } from "react-i18next";
import { Button, Card } from "react-bootstrap";
import { EmployeeList } from "./EmployeeList";

export const Step1SelectEmployees = ({
  recipients,
  onChange,
  onGoToAssignments,
  canGoToAssignments,
}) => {
  const { t } = useTranslation("EquipmentAndSoftwareApproval");

  return (
    <Card>
      <Card.Header>
        <h2 className="">{t("Step1.Title")}</h2>
      </Card.Header>
      <Card.Body>
        <p className="description">{t("Step1.Description")}</p>
        <div className="recipients-wrapper">
          <EmployeeList employees={recipients} onChange={onChange} />
        </div>
      </Card.Body>
      <Card.Footer>
        <div className="d-flex">
          <Button onClick={onGoToAssignments} disabled={!canGoToAssignments}>
            {t("Step1.ButtonGoToAssignments")}
          </Button>
        </div>
      </Card.Footer>
    </Card>
  );
};

Step1SelectEmployees.propTypes = {
  recipients: PropTypes.arrayOf(
    PropTypes.shape({
      id: PropTypes.number.isRequired,
      name: PropTypes.string.isRequired,
      email: PropTypes.string.isRequired,
    })
  ),
  onChange: PropTypes.func.isRequired,
  onGoToAssignments: PropTypes.func.isRequired,
  canGoToAssignments: PropTypes.bool.isRequired,
};

Step1SelectEmployees.defaultProps = {
  recipients: [],
};
