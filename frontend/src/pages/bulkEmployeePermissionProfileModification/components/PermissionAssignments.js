import React from "react";
import PropTypes from "prop-types";
import { Button, Card } from "react-bootstrap";
import { useTranslation } from "react-i18next";
import { PermissionAssignmentsTable } from "./PermissionAssignmentsTable";

export const PermissionAssignments = ({
  users,
  profiles,
  onChange,
  onSubmitAssignments,
  canSubmitAssignments,
}) => {
  const { t } = useTranslation("BulkEmployeePermissionProfileModification");
  return (
    <Card>
      <Card.Header>
        <h2 className="">{t("PermissionAssignments.Title")}</h2>
      </Card.Header>
      <Card.Body>
        <p className="description">{t("PermissionAssignments.Description")}</p>
        <PermissionAssignmentsTable
          users={users}
          profiles={profiles}
          onChange={onChange}
        />
      </Card.Body>
      <Card.Footer>
        <Button onClick={onSubmitAssignments} disabled={!canSubmitAssignments}>
          {t("PermissionAssignments.ButtonSendAssignments")}
        </Button>
      </Card.Footer>
    </Card>
  );
}

PermissionAssignments.propTypes = {
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

PermissionAssignments.defaultProps = {
  users: [],
  profiles: [],
};
