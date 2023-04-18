import React from "react";
import PropTypes from "prop-types";
import { useTranslation } from "react-i18next";

export const EmployeeList = ({ employees }) => {
  const { t } = useTranslation("EmployeeMonitoring");

  return (
    <table className="bordered table align-middle">
      <thead>
        <tr>
          <th>{t("EmployeeList.Name")}</th>
          <th>{t("EmployeeList.Email")}</th>
        </tr>
      </thead>
      <tbody>
        {employees.map((employee) => (
          <tr key={employee.id}>
            <td>{employee.name}</td>
            <td>{employee.email}</td>
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
      name: PropTypes.string.isRequired,
      email: PropTypes.string.isRequired,
    })
  ),
};

EmployeeList.defaultProps = {
  employees: [],
};
