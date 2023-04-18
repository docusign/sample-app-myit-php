/* eslint-disable jsx-a11y/label-has-associated-control */
import React from "react";
import PropTypes from "prop-types";
import { useTranslation } from "react-i18next";
import { Checkbox } from "../../../components";

export const EmployeeList = ({ employees, onChange }) => {
  const { t } = useTranslation("EquipmentAndSoftwareApproval");

  const onChangeInternal = (id, event) =>
    onChange({
      id,
      name: event.target.name,
      value: event.target.checked,
    });

  const isAssigned = (employee) =>
    (employee.software && employee.software.length > 0) ||
    (employee.equipment && employee.equipment.length > 0);

  return (
    <table className="bordered table align-middle">
      <thead>
        <tr>
          <th />
          <th>{t("EnvelopeList.Name")}</th>
          <th>{t("EnvelopeList.Email")}</th>
          <th>{t("EnvelopeList.SoftwareEquipment")}</th>
        </tr>
      </thead>
      <tbody>
        {employees.map((employee) => (
          <tr key={employee.id}>
            <td>
              <Checkbox
                id={`employee-email-${employee.id}`}
                name="selected"
                onChange={(event) => onChangeInternal(employee.id, event)}
                value={employee.selected}
              />
            </td>
            <td>{employee.name}</td>
            <td>{employee.email}</td>
            <td>
              {isAssigned(employee) && (
                <span className="badge bg-dark">
                  {t("EnvelopeList.Assigned")}
                </span>
              )}
            </td>
          </tr>
        ))}
      </tbody>
    </table>
  );
};

EmployeeList.propTypes = {
  employees: PropTypes.arrayOf(
    PropTypes.shape({
      id: PropTypes.number.isRequired,
      selected: PropTypes.bool.isRequired,
      name: PropTypes.string.isRequired,
      email: PropTypes.string.isRequired,
    })
  ),
  onChange: PropTypes.func.isRequired,
};

EmployeeList.defaultProps = {
  employees: [],
};
