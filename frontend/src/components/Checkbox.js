/* eslint-disable jsx-a11y/label-has-associated-control */
import React from "react";
import PropTypes from "prop-types";

export const Checkbox = ({ id, name, onChange, value }) => (
  <div className="checkbox">
    <input
      id={id}
      name={name}
      type="checkbox"
      onChange={onChange}
      checked={value}
    />
    <label htmlFor={id} />
  </div>
);

Checkbox.propTypes = {
  id: PropTypes.string.isRequired,
  name: PropTypes.string.isRequired,
  onChange: PropTypes.func.isRequired,
  value: PropTypes.bool.isRequired,
};
