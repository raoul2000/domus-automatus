# Services

## Ping

- method : GET
- headers:
    - `X-AuthKey` : the configured authentication key
- query param :
  - `action=ping`

## Send SMS

- method : GET
- headers:
    - `X-AuthKey` : the configured authentication key
- query param :
  - `action=send-sms`
  - `slient=true` : Optional - does not send SMS, just check input args and log


