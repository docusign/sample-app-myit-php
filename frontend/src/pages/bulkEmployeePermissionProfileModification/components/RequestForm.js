import React from "react";
import PropTypes from "prop-types";
import { useTranslation } from "react-i18next";
import { PermissionAssignments } from "./PermissionAssignments";

export const RequestForm = ({
  users,
  profiles,
  onChange,
  onSubmitAssignments,
  canSubmitAssignments,
}) => {
  const { t } = useTranslation("BulkEmployeePermissionProfileModification");

  return (
    <div className="request-form-card col-lg-8">
      <h1 className="mb-4">{t("Title")}</h1>
      <div className="permission-assignments-holder">
        <PermissionAssignments
          users={users}
          profiles={profiles}
          onChange={onChange}
          onSubmitAssignments={onSubmitAssignments}
          canSubmitAssignments={canSubmitAssignments}
        />
      </div>
    </div>
  );
}

RequestForm.propTypes = {
  users: PropTypes.arrayOf(
    PropTypes.shape({
      id: PropTypes.number.isRequired,
      name: PropTypes.string.isRequired,
      profileId: PropTypes.number.isRequired,
    })
  ),
  profiles: PropTypes.arrayOf(
    PropTypes.shape({
      label: PropTypes.string.isRequired,
      value: PropTypes.number.isRequired,
    })
  ),
  onChange: PropTypes.func.isRequired,
  onSubmitAssignments: PropTypes.func.isRequired,
  canSubmitAssignments: PropTypes.bool.isRequired,
};

RequestForm.defaultProps = {
  users: [],
  profiles: [],
};
