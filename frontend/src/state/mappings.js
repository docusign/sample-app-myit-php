import {
  EVENT_USER_UPDATED,
  EVENT_ENVELOPE_SENT,
  EVENT_ENVELOPE_DELIVERED,
  EVENT_ENVELOPE_SIGNED,
  EVENT_ENVELOPE_CREATED,
  EVENT_RECIPIENT_SENT,
  EVENT_RECIPIENT_DELIVERED,
  EVENT_RECIPIENT_SIGNED,
  EVENT_ENVELOPE_VOIDED,
  EVENT_ENVELOPE_RESENT,
  EVENT_ENVELOPE_CORRECTED,
  EVENT_RECIPIENT_RESENT,
  EVENT_RECIPIENT_REASSIGN,
  EVENT_RECIPIENT_FINUSH_LATER,
  EVENT_ENVELOPE_DECLINED,
  EVENT_ENVELOPE_DELETED,
  EVENT_RECIPIENT_DECLINED,
  ALERT_TYPE_SUCCESS,
  ALERT_TYPE_WARNING,
  ALERT_TYPE_DANGER,
  ALERT_TYPE_UNKNOWN,
} from "./constants";

const mapToAlertType = (eventName) => {
  switch (eventName) {
    case EVENT_USER_UPDATED:
    case EVENT_ENVELOPE_SENT:
    case EVENT_ENVELOPE_DELIVERED:
    case EVENT_ENVELOPE_SIGNED:
    case EVENT_ENVELOPE_CREATED:
    case EVENT_RECIPIENT_SENT:
    case EVENT_RECIPIENT_DELIVERED:
    case EVENT_RECIPIENT_SIGNED:
      return ALERT_TYPE_SUCCESS;
    case EVENT_ENVELOPE_VOIDED:
    case EVENT_ENVELOPE_RESENT:
    case EVENT_ENVELOPE_CORRECTED:
    case EVENT_RECIPIENT_RESENT:
    case EVENT_RECIPIENT_REASSIGN:
    case EVENT_RECIPIENT_FINUSH_LATER:
      return ALERT_TYPE_WARNING;
    case EVENT_ENVELOPE_DECLINED:
    case EVENT_ENVELOPE_DELETED:
    case EVENT_RECIPIENT_DECLINED:
      return ALERT_TYPE_DANGER;
    default:
      console.log(`Event with name: '${eventName}' is not defined`);
      return ALERT_TYPE_UNKNOWN;
  }
};

export const mapIncomingAlert = (alert) => ({
  id: `${alert.user}_${alert.createdAt}_${alert.event}`,
  user: alert.user,
  type: mapToAlertType(alert.event),
  localizationKey: alert.event,
  timestamp: alert.createdAt,
  event: alert.event
});
