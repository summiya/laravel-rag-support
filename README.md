#  AI RAG Support System

AI-powered support assistant built with Laravel, Ollama, PostgreSQL, pgvector, and Retrieval-Augmented Generation (RAG).

---

# Overview

 RAG is a scalable AI support platform designed to provide intelligent, context-aware customer support using local LLMs and vector search.

The system combines:

- Laravel backend architecture
- Local LLM inference using Ollama + Llama
- PostgreSQL with pgvector
- Retrieval-Augmented Generation (RAG)
- Semantic search
- Document ingestion pipelines
- AI-powered conversational support

The goal is to create a production-ready AI support infrastructure that can scale from a local development environment to enterprise-grade deployments.

---

# Features

## AI Support Chat
- Conversational AI support assistant
- Real-time chat interface
- Context-aware responses
- Session-based conversations

## RAG (Retrieval-Augmented Generation)
- Semantic document retrieval
- Vector similarity search
- Context injection into prompts
- Grounded AI responses using business data

## Local AI Infrastructure
- Runs locally using Ollama
- No external AI API required
- Privacy-friendly architecture
- Reduced operational cost

## Scalable Architecture
- Queue-based document processing
- Chunked ingestion pipelines
- Modular service architecture
- Extensible vector search layer

## Knowledge Base
- Support documentation indexing
- FAQ ingestion
- Product documentation support
- Internal knowledge retrieval

---

# Tech Stack

## Backend
- Laravel
- PHP 8+

## AI / LLM
- Ollama
- Llama 3

## Database
- PostgreSQL
- pgvector

## Frontend
- Blade
- Vanilla JavaScript

## Infrastructure
- Laravel Queues
- Background Jobs
- REST APIs

---

# Architecture

```text
User Question
      ↓
Laravel Application
      ↓
Embedding Generation
      ↓
pgvector Similarity Search
      ↓
Retrieve Relevant Chunks
      ↓
Inject Context into Prompt
      ↓
Llama 3 via Ollama
      ↓
AI Response
```

---

# Project Structure

```text
app/
├── Console/
├── Http/
│   └── Controllers/
├── Jobs/
├── Models/
├── Services/
│   ├── AI/
│   ├── Embeddings/
│   ├── Retrieval/
│   └── RAG/
├── Support/
└── Vector/

resources/
└── views/
    └── support/

database/
├── migrations/
└── seeders/
```

---

# Planned Components

## Chat System
- AI support interface
- Conversation memory
- Multi-session support
- Streaming responses

## Vector Search Engine
- pgvector integration
- Embedding indexing
- Top-K similarity search
- Metadata filtering

## Embedding Pipeline
- Document chunking
- Embedding generation
- Background indexing jobs
- Queue processing

## Knowledge Base Management
- File uploads
- PDF ingestion
- Markdown ingestion
- HTML extraction
- CMS synchronization

## AI Prompt Layer
- Prompt templates
- Context injection
- System prompt management
- Hallucination reduction

---

# Scalability Goals

The platform is designed for future scaling:

- Multi-tenant support
- Distributed vector infrastructure
- Dedicated AI inference servers
- Hybrid local/cloud LLM support
- Redis queue scaling
- Horizontal application scaling
- API-first architecture

---

# Local Development Setup

## Install Ollama

```bash
curl -fsSL https://ollama.com/install.sh | sh
```

## Pull Llama Model

```bash
ollama pull llama3
```

## Run Ollama

```bash
ollama run llama3
```

---

# PostgreSQL Setup

## Install PostgreSQL

```bash
brew install postgresql
```

## Start PostgreSQL

```bash
brew services start postgresql
```

---

# pgvector Setup

## Install pgvector

```bash
brew install pgvector
```

## Enable Extension

```sql
CREATE EXTENSION vector;
```

---

# Laravel Setup

## Install Dependencies

```bash
composer install
```

## Environment Setup

```bash
cp .env.example .env
php artisan key:generate
```

## Run Migrations

```bash
php artisan migrate
```

## Start Laravel

```bash
php artisan serve
```

---

# Current Status

## Completed
- Laravel AI support chat UI
- Ollama integration
- Local Llama inference
- AI conversational responses

## In Progress
- PostgreSQL integration
- pgvector support
- RAG pipeline implementation

## Planned
- Embedding generation
- Document ingestion
- Semantic retrieval
- Production deployment pipeline

---

# Vision

This project aims to evolve into a scalable AI-native support infrastructure capable of powering:

- Customer support assistants
- Internal AI knowledge bases
- AI help centers
- AI product assistants
- Enterprise RAG systems

---

# License

MIT
