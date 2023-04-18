import React from "react";
import PropTypes from "prop-types";
import { Button, Card } from "react-bootstrap";
import { useTranslation } from "react-i18next";
import { EmployeeAssignmentsList } from "./EmployeeAssignmentsList";

export const Step2AssignmentToEmployees = ({
  recipients,
  software,
  equipment,
  onChange,
  onBackToAssignments,
  onSubmitAssignments,
  canSubmitAssignments,
}) => {
  const { t } = useTranslation("EquipmentAndSoftwareApproval");
  return (
    <Card>
      <Card.Header>
        <h2 className="">{t("Step2.Title")}</h2>
      </Card.Header>
      <Card.Body>
        <p className="description">{t("Step2.Description")}</p>
        <EmployeeAssignmentsList
          recipients={recipients}
          software={software}
          equipment={equipment}
          onChange={onChange}
        />
      </Card.Body>
      <Card.Footer>
        <hr />
        <div className="d-flex gap-4">
          <Button
            className="btn-secondary btn-secondary-gray"
            onClick={onBackToAssignments}
          >
            <span className="gradient-text">{t("Step2.ButtonBack")}</span>
          </Button>
          <Button
            onClick={onSubmitAssignments}
            disabled={!canSubmitAssignments}
          >
            {t("Step2.ButtonSendAssignments")}
          </Button>
        </div>
      </Card.Footer>
    </Card>
  );
};

Step2AssignmentToEmployees.propTypes = {
  recipients: PropTypes.arrayOf(
    PropTypes.shape({
      id: PropTypes.number.isRequired,
      name: PropTypes.string.isRequired,
      email: PropTypes.string.isRequired,
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
  onChange: PropTypes.func.isRequired,
  onBackToAssignments: PropTypes.func.isRequired,
  onSubmitAssignments: PropTypes.func.isRequired,
  canSubmitAssignments: PropTypes.bool.isRequired,
};

Step2AssignmentToEmployees.defaultProps = {
  recipients: [],
  software: [],
  equipment: [],
};
