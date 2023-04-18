import React from "react";
import PropTypes from "prop-types";
import classNames from "classnames";
import Select from "react-select";
import { useTranslation } from "react-i18next";

export const PermissionAssignmentsTable = ({ users, profiles, onChange }) => {
  const { t } = useTranslation("BulkEmployeePermissionProfileModification");

  const onChangeInternal = (id, name, event) => {
    onChange({
      id,
      name,
      value: event.value,
    });
  };

  const getDefaultValue = (options, value) =>
    options.find((op) => op.value === value);

  return (
    <table className="bordered table align-middle">
      <thead>
        <tr>
          <th style={{ width: "50%" }}>
            {t("PermissionAssignmentsTable.Name")}
          </th>
          <th style={{ width: "50%" }}>
            {t("PermissionAssignmentsTable.Profile")}
          </th>
        </tr>
      </thead>
      <tbody>
        {users.map((user) => (
          <tr
            key={user.id}
            className={classNames({ "is-dirty": user.isDirty })}
          >
            <td>{user.name}</td>
            <td>
              <Select
                className="react-select-container"
                classNamePrefix="react-select"
                options={profiles}
                value={getDefaultValue(profiles, user.profileId)}
                onChange={(event) =>
                  onChangeInternal(user.id, "profileId", event)
                }
              />
            </td>
          </tr>
        ))}
      </tbody>
    </table>
  );
}

PermissionAssignmentsTable.propTypes = {
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
};

PermissionAssignmentsTable.defaultProps = {
  users: [],
  profiles: [],
};
