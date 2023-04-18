export const LOADING_STATE_IDLE = "IDLE";
export const LOADING_STATE_PENDING = "PENDING";
export const LOADING_STATE_SUCCESS = "SUCCESS";
export const LOADING_STATE_FAILED = "FAILED";

export const ALERT_TYPE_SUCCESS = "success";
export const ALERT_TYPE_WARNING = "warning";
export const ALERT_TYPE_DANGER = "danger";
export const ALERT_TYPE_UNKNOWN = "unknown";

export const EVENT_USER_UPDATED = "User_Updated";

export const EVENT_ENVELOPE_SENT = "Envelope_Sent";
export const EVENT_ENVELOPE_DELIVERED = "Envelope_Delivered";
export const EVENT_ENVELOPE_SIGNED = "Envelope_Signed";
export const EVENT_ENVELOPE_DECLINED = "Envelope_Declined";
export const EVENT_ENVELOPE_VOIDED = "Envelope_Voided";
export const EVENT_ENVELOPE_RESENT = "Envelope_Resent";
export const EVENT_ENVELOPE_CORRECTED = "Envelope_Corrected";
export const EVENT_ENVELOPE_DELETED = "Envelope_Deleted";
export const EVENT_ENVELOPE_CREATED = "Envelope_Created";

export const EVENT_RECIPIENT_SENT = "Recipient_Sent";
export const EVENT_RECIPIENT_DELIVERED = "Recipient_Delivered";
export const EVENT_RECIPIENT_SIGNED = "Recipient_Signed";
export const EVENT_RECIPIENT_DECLINED = "Recipient_Declined";
export const EVENT_RECIPIENT_RESENT = "Recipient_Resent";
export const EVENT_RECIPIENT_REASSIGN = "Recipient_Reassign";
export const EVENT_RECIPIENT_FINUSH_LATER = "Recipient_FinishLater";

export const initialState = {
  loading: LOADING_STATE_IDLE,
  users: [],
  initialUsers: [],
  historyUsers: [],
  software: [],
  equipment: [],
  profiles: [],
  alerts: [],
};
