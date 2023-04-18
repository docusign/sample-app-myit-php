import React from "react";
import PropTypes from "prop-types";
import { useTranslation } from "react-i18next";
import {
  Step1SelectEmployeesIndex,
  Step2AssignmentToEmployeesIndex,
} from "../consts";

import { Step1SelectEmployees } from "./Step1SelectEmployees";
import { Step2AssignmentToEmployees } from "./Step2AssignmentToEmployees";

export const RequestForm = ({
  currentStepIndex,
  recipients,
  selectedRecipients,
  software,
  equipment,
  onChange,
  onGoToAssignments,
  canGoToAssignments,
  onBackToAssignments,
  onSubmitAssignments,
  canSubmitAssignments,
}) => {
  const { t } = useTranslation("EquipmentAndSoftwareApproval");

  return (
    <div className="request-form-card col-lg-8">
      <h1 className="mb-4">{t("Title")}</h1>
      <div className="wizard-holder">
        {currentStepIndex === Step1SelectEmployeesIndex && (
          <Step1SelectEmployees
            recipients={recipients}
            onChange={onChange}
            onGoToAssignments={onGoToAssignments}
            canGoToAssignments={canGoToAssignments}
          />
        )}
        {currentStepIndex === Step2AssignmentToEmployeesIndex && (
          <Step2AssignmentToEmployees
            recipients={selectedRecipients}
            software={software}
            equipment={equipment}
            onChange={onChange}
            onBackToAssignments={onBackToAssignments}
            onSubmitAssignments={onSubmitAssignments}
            canSubmitAssignments={canSubmitAssignments}
          />
        )}
      </div>
    </div>
  );
};

RequestForm.propTypes = {
  currentStepIndex: PropTypes.number.isRequired,
  recipients: PropTypes.arrayOf(
    PropTypes.shape({
      id: PropTypes.number.isRequired,
      name: PropTypes.string.isRequired,
      email: PropTypes.string.isRequired,
    })
  ),
  selectedRecipients: PropTypes.arrayOf(
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
  onGoToAssignments: PropTypes.func.isRequired,
  canGoToAssignments: PropTypes.bool.isRequired,
  onBackToAssignments: PropTypes.func.isRequired,
  onSubmitAssignments: PropTypes.func.isRequired,
  canSubmitAssignments: PropTypes.bool.isRequired,
};

RequestForm.defaultProps = {
  recipients: [],
  selectedRecipients: [],
  software: [],
  equipment: [],
};
