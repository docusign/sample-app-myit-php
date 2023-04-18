import React from "react";
import PropTypes from "prop-types";
import { useTranslation } from "react-i18next";
import { Form } from "react-bootstrap";
import Select from "react-select";

export const EmployeeAssignmentsList = ({
  recipients,
  software,
  equipment,
  onChange,
}) => {
  const onChangeInternalSelect = (id, name, event) => {
    onChange({
      id,
      name,
      value: event.map((v) => v.value),
    });
  };

  const onChangeInternal = (id, event) =>
    onChange({
      id,
      name: event.target.name,
      value: event.target.value,
    });

  const { t } = useTranslation("EquipmentAndSoftwareApproval");

  const getDefaultValue = (options, values) =>
    values ? options.filter((s) => values.includes(s.value)) : [];

  return (
    <div className="employee-assignments-list-wrapper">
      {recipients.map((recipient) => (
        <div className="employee-assignments-wrapper" key={recipient.id}>
          <h3>{recipient.name}</h3>
          <div className="row employee-assignments-fields">
            <div className="col-lg-5">
              <Form.Group controlId={`recipient-email-${recipient.id}`}>
                <Form.Label>{t("EmployeeAssignmentsList.Email")}</Form.Label>
                <Form.Control
                  className="input-text-m"
                  type="text"
                  name="email"
                  value={recipient.email}
                  onChange={(event) => onChangeInternal(recipient.id, event)}
                />
              </Form.Group>
            </div>
            <div className="col-lg-7">
              <Form.Group controlId={`recipient-email-${recipient.id}`}>
                <Form.Label>
                  {t("EmployeeAssignmentsList.SoftwareAndRequipment")}
                </Form.Label>
                <Select
                  placeholder="Select Software License "
                  className="react-select-container mb-2"
                  classNamePrefix="react-select"
                  options={software}
                  defaultValue={getDefaultValue(software, recipient.software)}
                  onChange={(event) =>
                    onChangeInternalSelect(recipient.id, "software", event)
                  }
                  isMulti
                />
                <Select
                  placeholder="Select Equipment"
                  className="react-select-container"
                  classNamePrefix="react-select"
                  options={equipment}
                  defaultValue={getDefaultValue(equipment, recipient.equipment)}
                  onChange={(event) =>
                    onChangeInternalSelect(recipient.id, "equipment", event)
                  }
                  isMulti
                />
              </Form.Group>
            </div>
          </div>
        </div>
      ))}
    </div>
  );
};

EmployeeAssignmentsList.propTypes = {
  recipients: PropTypes.arrayOf(
    PropTypes.shape({
      id: PropTypes.number.isRequired,
      name: PropTypes.string.isRequired,
      email: PropTypes.string.isRequired,
      equipment: PropTypes.arrayOf(PropTypes.number),
      software: PropTypes.arrayOf(PropTypes.number),
    })
  ),
  software: PropTypes.arrayOf(
    PropTypes.shape({
      label: PropTypes.string.isRequired,
      value: PropTypes.number.isRequired,
    })
  ).isRequired,
  equipment: PropTypes.arrayOf(
    PropTypes.shape({
      label: PropTypes.string.isRequired,
      value: PropTypes.number.isRequired,
    })
  ).isRequired,
  onChange: PropTypes.func.isRequired,
};

EmployeeAssignmentsList.defaultProps = {
  recipients: [],
};
