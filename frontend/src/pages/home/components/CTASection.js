import React from "react";
import PropTypes from "prop-types";

export const CTASection = ({ title, description, primaryLink, secondaryLink }) => (
    <section className="cta-section text-center">
      <div className="container">
        <h2 className="h2 cta-title">{title}</h2>
        <div className="cta-description">{description}</div>
        <div className="cta-button-holder">
          <a href={primaryLink.href} target="_blank" rel="noopener noreferrer">
            <button type="button" className="btn btn-primary cta-btn-1">
              {primaryLink.name}
            </button>
          </a>
          <a
            href={secondaryLink.href}
            target="_blank"
            rel="noopener noreferrer"
          >
            <button type="button" className="btn btn-secondary cta-btn-2">
              <span className="gradient-text">{secondaryLink.name}</span>
            </button>
          </a>
        </div>
      </div>
    </section>
  )

CTASection.propTypes = {
  title: PropTypes.string.isRequired,
  description: PropTypes.oneOfType([PropTypes.string, PropTypes.element])
    .isRequired,
  primaryLink: PropTypes.shape({
    name: PropTypes.string.isRequired,
    href: PropTypes.string.isRequired,
  }).isRequired,
  secondaryLink: PropTypes.shape({
    name: PropTypes.string.isRequired,
    href: PropTypes.string.isRequired,
  }).isRequired,
};
