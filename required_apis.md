# FitPaxPro - Required API Documentation (Unimplemented)

This document outlines the APIs required by the FitPaxPro Mobile App that are not yet implemented in the Laravel backend. Each endpoint includes the expected request and response payloads.

---

## 1. Gym Management & Operations

### A. Dashboard Summary
**Endpoint**: `GET /api/gym/dashboard/summary`  
**Purpose**: Consolidates KPIs for the Gym Owner's main screen.

**Request (Query)**:
```json
{
  "gym_id": "string (UUID)"
}
```

**Response (200 OK)**:
```json
{
  "success": true,
  "data": {
    "stats": {
      "active_members": 142,
      "check_ins_today": 32,
      "monthly_revenue": 45000.00,
      "pending_enquiries": 5
    },
    "recent_activity": [
      { "time": "2 mins ago", "message": "Aman Verma checked in", "type": "attendance" },
      { "time": "1 hr ago", "message": "New subscription: Elite Plan", "type": "payment" }
    ],
    "attendance_trend": [12, 45, 67, 32, 88, 54, 32]
  }
}
```

---

### B. Member Attendance
**Endpoint**: `POST /api/gym/attendance/check-in`  
**Purpose**: Registers a member's visit to the gym.

**Request (Body)**:
```json
{
  "gym_id": "string (UUID)",
  "user_id": "string (UUID)",
  "method": "string (qr_code|manual|biometric)",
  "latitude": "decimal (optional)",
  "longitude": "decimal (optional)"
}
```

**Response (201 Created)**:
```json
{
  "success": true,
  "message": "Check-in successful.",
  "data": {
    "attendance_id": "string (UUID)",
    "check_in_time": "2026-05-15 08:30:15",
    "member_name": "John Doe"
  }
}
```

---

### C. Provision Sections
**Endpoint**: `GET /api/provisions/sections`  
**Purpose**: Dynamically configures the app's available modules based on gym settings.

**Response (200 OK)**:
```json
{
  "success": true,
  "data": [
    { "id": "attendance", "title": "Attendance", "icon": "fact_check", "route": "/attendance", "color": "#FCA5A5" },
    { "id": "members", "title": "Members", "icon": "people", "route": "/members", "color": "#7DD3FC" },
    { "id": "classes", "title": "Classes", "icon": "fitness_center", "route": "/classes", "color": "#5EEAD4" },
    { "id": "reports", "title": "Reports", "icon": "analytics", "route": "/reports", "color": "#B794F4" }
  ]
}
```

---

## 2. Personalization & Training

### A. Diet Plans
**Endpoint**: `GET /api/user-app/profile/diet-plans`  
**Purpose**: Fetches the assigned meal plan for a member.

**Response (200 OK)**:
```json
{
  "success": true,
  "data": {
    "plan_name": "Muscle Bulk 3000",
    "calories_target": 3000,
    "macros": { "protein": "180g", "carbs": "350g", "fats": "80g" },
    "meals": [
      { "time": "Breakfast", "food": "6 Egg Whites, 100g Oats", "calories": 550 },
      { "time": "Post-Workout", "food": "Whey Protein, 1 Banana", "calories": 300 }
    ]
  }
}
```

---

### B. Workout Split / Exercise Plans
**Endpoint**: `GET /api/user-app/profile/exercise-plans`  
**Purpose**: Fetches the weekly workout schedule for a member.

**Response (200 OK)**:
```json
{
  "success": true,
  "data": {
    "title": "5-Day Hypertrophy Split",
    "difficulty": "Advanced",
    "schedule": [
      {
        "day": "Monday",
        "muscle_group": "Chest & Triceps",
        "exercises": [
          { "name": "Incline Bench Press", "sets": 4, "reps": "8-10", "rest": "90s" },
          { "name": "Cable Flyes", "sets": 3, "reps": "15", "rest": "60s" }
        ]
      }
    ]
  }
}
```

---

## 3. Communication & Engagement

### A. Gym Enquiries
**Endpoint**: `POST /api/gym/enquiries`  
**Purpose**: Allows potential members to ask questions to a gym owner.

**Request (Body)**:
```json
{
  "gym_id": "string (UUID)",
  "subject": "string",
  "message": "string",
  "enquiry_type": "string (membership_plans|facilities|trial_request)"
}
```

**Response (200 OK)**:
```json
{
  "success": true,
  "message": "Enquiry sent successfully. The gym will contact you shortly."
}
```

---

### B. Gym Reviews
**Endpoint**: `POST /api/gym/reviews`  
**Purpose**: Submitting member feedback.

**Request (Body)**:
```json
{
  "gym_id": "string (UUID)",
  "rating": "integer (1-5)",
  "comment": "string",
  "anonymous": "boolean"
}
```

**Response (201 Created)**:
```json
{
  "success": true,
  "message": "Review published."
}
```

---

## 4. Notifications & Reports

### A. FCM Token Registration
**Endpoint**: `POST /api/fcm/register-token`  
**Purpose**: Links a device token to a user for push notifications.

**Request (Body)**:
```json
{
  "token": "string (FCM Token)",
  "device_name": "string (e.g., iPhone 15 Pro)"
}
```

**Response (200 OK)**:
```json
{
  "success": true,
  "message": "FCM Token updated."
}
```

### B. Operations Reports
**Endpoint**: `GET /api/gym/reports/revenue`  
**Purpose**: Fetches historical revenue data for chart plotting.

**Request (Query)**:
```json
{
  "gym_id": "string (UUID)",
  "period": "string (weekly|monthly|yearly)"
}
```

**Response (200 OK)**:
```json
{
  "success": true,
  "data": {
    "total_revenue": 125000.00,
    "labels": ["Jan", "Feb", "Mar", "Apr"],
    "values": [25000, 30000, 28000, 42000]
  }
}
```
